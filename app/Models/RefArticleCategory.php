<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RefArticleCategory extends Pivot
{
    protected $table = 'ref_article_categories';
}
