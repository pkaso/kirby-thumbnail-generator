<?php

class GenerationResult {

  protected $result = [];
  protected $status;

  const STATUS_SKIP = 'skip';
  const STATUS_ERROR = 'error';
  const STATUS_SUCCESS = 'ok';

  public function success($preset, $thumbnail) {
    $this->status = self::STATUS_SUCCESS;
    $this->result[$preset] = $thumbnail->niceSize();
    return $this;
  }

  public function error($message) {
    $this->status = self::STATUS_ERROR;
    $this->result = ["message"=>$message];
    return $this;
  }

  public function skip($image) {
    $this->status = self::STATUS_SKIP;
    $this->result = ["message"=>"Image '{$image->filename()}' skipped."];
    return $this;
  }

  public function toArray() {
    return [
      'status' => $this->status,
      'result' => $this->result
    ];
  }
}

$kirby->set('route', array(
    'pattern' => 'api/thumbnails/gui',
    'action' => function () {
        if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'kirby-thumbnail-generator.html')) {
          return new response(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'kirby-thumbnail-generator.html'));
        }
    }
));

$kirby->set('route', array(
    'pattern' => 'api/thumbnails/list',
    'action' => function () {
        $images = array();
        foreach(site()->index()->images() as $filename => $image) {
            $images[] = [
              'path' => $filename,
              'name' => $image->filename(),
              'size' => $image->niceSize()
            ];
        }
        return response::json($images);
    }
));

$kirby->set('route', array(
        'pattern' => 'api/thumbnails/generate/(:any)',
        'action' => function ($base64path) {

            $result = new GenerationResult();

            $generate = c::get('thumbnails.generate');
            if (is_callable($generate)) {
              $path =  base64_decode($base64path);
              $pathInfo = pathinfo($path);

              $page = site()->find($pathInfo['dirname']);
              if ($page) {
                $image = $page->image($pathInfo['basename']);
                if ($image) {
                  $result = $generate($image, $page, $result);
                } else {
                  $result->error("Image '{$pathInfo['basename']}' doesn't exists.");
                }
              } else {
                $result->error("Page '{$pathInfo['dirname']}' doesn't exists.");
              }
            } else {
              $result->error("Function 'thumbnails.generate' doesn't exists.");
            }
            return response::json($result->toArray());
        }
    )
);
