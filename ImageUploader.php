<?php

namespace MyApp;

/**
 *
 */
class ImageUploader {

  private $_imageFileName;
  private $_imageType;

  public function upload(){
    try {
      /*error check*/
      $this->_validateUpload();
      /*type check*/
      $ext = $this->_validateImageType();
      /*save*/
      $savePath = $this->_save($ext);
      /*create thumbanil*/
      $this->_createThumbnail($savePath);

      $_SESSION['success'] = 'Upload Done!';

    } catch (\Exception $e) {
      $_SESSION['success'] = $e->getMessage();
      // exit;
    }

    /*redirect 一連の処理が終わったら、index.phpに戻りたいのでリダイレクトを使う。*/
    /*$_SERVER['HTTP_HOST']は表示中のページのアドレスを返す。
    $_SERVERに表示中のアドレスが格納される。HTTP か　HTTPS か判定する。HOST　サーバーのホスト名を取得。
    XAMPPだとdashboardに戻るので、URLで対処。*/
    header('Location: http://localhost/board_php/index.php');
    exit;
  }

    public function getResults() {
      $success = null;
      $error = null;
      if (isset($_SESSION['success'])) {
          $success = $_SESSION['success'];
          unset($_SESSION['success']);
      }
      if (isset($_SESSION['error'])) {
          $success = $_SESSION['error'];
          unset($_SESSION['error']);
      }
        return [$success, $error];
    }

    public function getImages() {
      $images = [];
      $files = [];
      $imageDir = opendir(IMAGES_DIR);
      while (false !== ($file = readdir($imageDir))) {
        if ($file === '.' || $file === '..'){
          continue;
        }
        $files[] = $file;
        if (file_exists(THUMBNAIL_DIR . '/' . $file)) {
          $images[] = basename(THUMBNAIL_DIR) . '/' . $file;
        }else {
          $images[] = basename(IMAGES_DIR) . '/' . $file;
        }
      }
      array_multisort($files, SORT_DESC, $images);
      return $images;
    }



    private function _createThumbnail($savePath) {
      $imageSize = getimagesize($savePath);
      $width = $imageSize[0];
      $height = $imageSize[1];
      if ($width > THUMBNAIL_WIDTH) {
        $this->_createThumbnailMain($savePath, $width, $height);
      }
    }

    private function _createThumbnailMain($savePath, $width, $height) {
      switch($this->_imageType) {
        case IMAGETYPE_GIF:
          $srcImage = imagecreatefromgif($savePath);
          break;
        case IMAGETYPE_JPEG:
          $srcImage = imagecreatefromjpeg($savePath);
          break;
        case IMAGETYPE_PNG:
          $srcImage = imagecreatefrompng($savePath);
          break;
      }
      $thumbHeight = round($height * THUMBNAIL_WIDTH / $width);
      $thumbImage = imagecreatetruecolor(THUMBNAIL_WIDTH, $thumbHeight);
      imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, THUMBNAIL_WIDTH, $thumbHeight, $width, $height);

      switch ($this->_imageType) {
        case IMAGETYPE_GIF:
          imagegif($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
          break;
        case IMAGETYPE_JPEG:
          imagejpeg($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
          break;
        case IMAGETYPE_PNG:
          imagepng($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
          break;
      }

    }

    private function _save($ext) {
      $this->_imageFileName = sprintf(
        '%s_%s.%s',
        time(),
        sha1(uniqid(mt_rand(), true)),
        $ext
      );
      $savePath = IMAGES_DIR . '/' . $this->_imageFileName;
      $res = move_uploaded_file($_FILES['image']['tmp_name'], $savePath);
      if ($res === false) {
        throw new \Exception('could not Upload!');
      }
      return $savePath;
    }

    private function _validateImageType() {
      $this->_imageType = exif_imagetype($_FILES['image']['tmp_name']);
      switch ($this->_imageType) {
        case IMAGETYPE_GIF:
          return 'gif';
        case IMAGETYPE_JPEG:
          return 'jpg';
        case IMAGETYPE_PNG:
          return 'png';
        default:
          throw new \Exception('PNG/JPEG/GIF only');
      }
    }



    private function _validateUpload() {

    if (!isset($_FILES['image']) || !isset($_FILES['image']['error'])) {
      throw new \Exception('Upload Error!');
    }

    switch($_FILES['image']['error']) {
      case UPLOAD_ERR_OK:
        return true;
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        throw new \Exception('File too large!');
      default:
      throw new \Exception('エラー: 画像ファイルを選択してください。');
    }
  }
}


 ?>
