<?php

namespace App\View;

use Stephanie\View\View;

class AppView extends View {
    public function __construct($class = null)
    {
        parent::__construct($class);

        $this->addGlobal('app_theme'   , "united");
        $this->addGlobal('software'    , 'Web File Explorer');
        $this->addGlobal('software_v'  , 'v1');
        $this->addGlobal('format_date' , 'l, d F Y');
    }
}
