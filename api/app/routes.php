 <?php

use App\Controller\GetThumbnailsArchive;
use App\Controller\PostThumbnailsArchive;
use Slim\App;

return function (App $app) {
    $app->post('/thumbnails', PostThumbnailsArchive ::class);
    $app->get('/thumbnails/{archive}', GetThumbnailsArchive ::class);
};