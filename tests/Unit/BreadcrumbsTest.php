<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;
use Illuminate\Routing\Router;
use App\Breadcrumbs;
use Mockery as m;

class BreadcrumbsTest extends TestCase
{
    protected $request;

    protected $breadcrumbs;

    protected $config;

    protected $router;

    public function setUp()
    {
        $this->request = m::mock(Request::class);
        $this->router  = m::mock(Router::class);
        $this->config  = m::mock(Config::class);

        $breadcrumbConfig = [
             'sistema' => [
                'title' => 'Dashboard',
                'route' => '#'
             ],
             'administrators' => [
                 'title' => 'Administradores',
                 'route' => 'administrators'
             ],
            'editable' => [
                 'title' => 'Editable',
                 'route' => 'editable'
             ]
        ];

        $this->config->shouldReceive('get')
                     ->with('breadcrumbs')
                     ->andReturn($breadcrumbConfig);

        $this->breadcrumbs = new Breadcrumbs(
            $this->request, 
            $this->config,
            $this->router
        );
    }
    
    /** @test */
    public function should_return_array_of_links()
    {
        $this->request->shouldReceive('path')->andReturn('/');
        $this->router->shouldReceive('has')->andReturn(false);

        $result = $this->breadcrumbs->getLinks();
        
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }
    
    /** @test */
    public function should_return_request_path_components_without_system_fragment()
    {
        $this->request->shouldReceive('path')->andReturn('/sistema/administrators');
   
        $result = $this->breadcrumbs->getPathComponents();

        $this->assertInternalType('array', $result);
        $this->assertEquals(['administrators'], $result);
    }

    /** @test */
    public function should_return_array_with_captions_and_routes()
    {
        $this->request->shouldReceive('path')
                      ->andReturn('/sistema/administrators');
    
        $this->router->shouldReceive('has')
                     ->with('administrators.index')
                     ->andReturn(true);

        $result = $this->breadcrumbs->getLinks();

        $this->assertArrayHasKey('Administradores', $result);
        $this->assertContains('administrators.index', $result, '', false, true, true);
    }

    /** @test */
    public function should_create_edit_link_when_index_route_doesnt_exists()
    {
        $this->request->shouldReceive('path')
                      ->andReturn('/sistema/editable/1');

        $this->request->shouldReceive('all')
                      ->andReturn(['id' => 1]);  

        $this->router->shouldReceive('has')
                     ->with('editable.index')
                     ->andReturn(false);

        $this->router->shouldReceive('has')
                     ->with('editable.edit')
                     ->andReturn(true);

        $result = $this->breadcrumbs->getLinks();

        $this->assertArrayHasKey('Editable', $result);
        $this->assertArrayHasKey('Editar', $result);
        $this->assertContains(['editable.edit', ['id' => 1]], $result);
    }

    /** @test */
    public function should_return_title_when_fragment_is_create()
    {
        $this->request->shouldReceive('path')
                      ->andReturn('/sistema/create');

        $this->router->shouldReceive('has')
                     ->with('editable.index')
                     ->andReturn(false);

        $this->router->shouldReceive('has')
                     ->with('editable.edit')
                     ->andReturn(true);

        $result = $this->breadcrumbs->getLinks();

        $this->assertArrayHasKey('Novo', $result);
        $this->assertContains('#', $result);
    }

    /** @test */
    public function should_get_title_from_last_component_model_when_component_is_number()
    {

    }
}
