<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;
use Illuminate\Routing\Router;

class Breadcrumbs
{
    protected $request;

    protected $config;

    protected $router;

    protected $reservedKeys = [
        'create' => 'Novo',
        'edit'   => 'Editar'
    ];

    public function __construct(
        Request $request,
        Config  $config,
        Router  $router
    ) {
        $this->request = $request;
        $this->config  = $config->get('breadcrumbs');
        $this->router  = $router;  
    }

    public function getPathComponents()
    {
        $components = explode('/', $this->request->path());
        $components = array_filter($components, [$this, 'filterComponents']);
        $components = array_values($components); 
        
        return $components;
    }

    protected function filterComponents($item)
    {
        return (bool)strlen($item) && $item != 'sistema';
    }

    public function getLinks()
    {
        $components = $this->getPathComponents();
        $titles    = array_map([$this, 'getTitle'], $components);
        $routes    = array_map([$this, 'getRoute'], $components);

        return array_combine($titles, $routes);
    }
    
    protected function getTitle($item)
    {
        if (ctype_digit($item)) {
            return "Editar";
        }  

        if (array_key_exists($item, $this->reservedKeys)) {
            return $this->reservedKeys[$item];
        }

        return $this->config[$item]['title'];
    }

    protected function getRoute($item) {
        if (ctype_digit($item)) {
            return '#';
        }

        if (array_key_exists($item, $this->reservedKeys)) {
            return '#';
        }

        if ($this->router->has("$item.index")) {
            return "$item.index"; 
        }

        if ($this->router->has("$item.edit")) {
            return ["$item.edit", $this->request->all()]; 
        }

        return "#"; 
    }
}
