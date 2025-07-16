<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class SidebarLink extends Component
{
    /**
     * The link's URL.
     *
     * @var string
     */
    public $href;

    /**
     * Indicates if the link is currently active.
     *
     * @var bool
     */
    public $active;

    /**
     * Create the component instance.
     *
     * @param  string  $href
     * @param  bool  $active
     * @return void
     */
    public function __construct($href, $active = false)
    {
        $this->href = $href;
        $this->active = $active;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.sidebar-link');
    }
}