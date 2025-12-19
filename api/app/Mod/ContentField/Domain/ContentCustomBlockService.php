<?php
namespace App\Mod\ContentField\Domain;

use App\Domain\BaseService;
use App\Mod\ContentField\Domain\Models\ContentField;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property ContentField $model
 */
class ContentCustomBlockService extends BaseService
{
    public function __construct(ContentField $model)
    {
        parent::__construct($model);
    }

}
