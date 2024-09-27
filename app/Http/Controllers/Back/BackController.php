<?php

namespace App\Http\Controllers\Back;

use Session;
use App\Models\Module;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BackController extends Controller
{
   protected $has_module = NULL;
   protected $modules = NULL;

 	public function getSiteConfig()
 	{
		if (Schema::hasTable('product_mains')) {
 			parent::getProductMains();
		}
		parent::getModules();
 	}

}
