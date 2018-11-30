<?php

// EXAMPLE thumbnail presets, use our own :)
c::set('thumbs.presets', [
  'medium' =>   ['width' => 880,  'height' => 400,  'crop' => true, 'quality' => 60)],
  'fullhd' =>   ['width' => 1920, 'height' => null, 'quality' => 60],
  'profile' =>  ['width' => 560,  'height' => 760,  'crop' => true, 'quality' => 60],
]);

/**
 * function to generate thumbnils
 *
 * you can call result in these exmaples:
 * $result->skip($image)                - skip this file
 * $result->error($message)             - some error on this image
 * $result->succes($preset, $thumbnail) - success on thumb creation
 *
 *
 * @param $image  kirby image object
 * @param $page   kirby page where the image is located
 * @param $result result object
 *
 */
c::set('thumbnails.generate', function($image, $page, $result)
{

  // you can decide how to handle every page or image

  switch ($page->template()) {

    // skip this file example
    case 'contact':
      $result->skip($image);
      break;

    // one thumbnail example
    case 'about':
      $thumb = $image->thumb('profile');
      if ($thumb) {
        $result->success('profile', $thumb);
      }
      break;

    // two (or more) thumbnails examples
    default:
      $thumb = $image->thumb('medium');
      if ($thumb) {
        $result->success('medium', $thumb);
      }
      $thumb = $image->thumb('fullhd');
      if ($thumb) {
        $result->success('fullhd', $thumb);
      }
      break;
  }

  return $result;
});
