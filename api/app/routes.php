 <?php

use App\Application\Controller\Thumbnails\GetThumbnailsArchive;
use App\Application\Controller\Thumbnails\PostThumbnailsArchive;
use Slim\App;

return function (App $app) {
    $app->post('/thumbnails', PostThumbnailsArchive ::class);
    $app->get('/thumbnails/{archive}', GetThumbnailsArchive ::class);
};