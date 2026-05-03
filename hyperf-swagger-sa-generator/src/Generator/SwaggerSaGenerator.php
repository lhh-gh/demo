<?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace LhhGh\HyperfSwaggerSaGenerator\Generator;                                                                                                                              
                                                                                                                                                                                   
  use Hyperf\Stringable\Str;                                                                                                                                                       
                                                                                                                                                                                   
  class SwaggerSaGenerator                                                                                                                                                         
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected string $packageBasePath,                                                                                                                                       
          protected string $projectBasePath                                                                                                                                        
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function generate(string $name, ?string $tag = null, ?string $prefix = null, array $only = []): string                                                                
      {                                                                                                                                                                            
          $className = Str::studly($name) . 'Controller';                                                                                                                          
          $tagName = $tag ?: Str::studly($name);                                                                                                                                   
          $routePrefix = $prefix ?: Str::snake($name, '-');                                                                                                                        
                                                                                                                                                                                   
          $methods = $this->buildMethods($routePrefix, $tagName, $only);                                                                                                           
                                                                                                                                                                                   
          $stub = file_get_contents($this->packageBasePath . '/stubs/controller.stub');                                                                                            
                                                                                                                                                                                   
          $content = str_replace(                                                                                                                                                  
              ['{{ControllerName}}', '{{Methods}}'],                                                                                                                               
              [$className, $methods],                                                                                                                                              
              $stub                                                                                                                                                                
          );                                                                                                                                                                       
                                                                                                                                                                                   
          $targetDir = $this->projectBasePath . '/app/Controller';                                                                                                                 
          if (! is_dir($targetDir)) {                                                                                                                                              
              mkdir($targetDir, 0777, true);                                                                                                                                       
          }                                                                                                                                                                        
                                                                                                                                                                                   
          $targetFile = $targetDir . '/' . $className . '.php';                                                                                                                    
          file_put_contents($targetFile, $content);                                                                                                                                
                                                                                                                                                                                   
          return $targetFile;                                                                                                                                                      
      }                                                                                                                                                                            
                                                                                                                                                                                   
      protected function buildMethods(string $routePrefix, string $tagName, array $only): string                                                                                   
      {                                                                                                                                                                            
          $only = empty($only) ? ['list', 'detail', 'create', 'update', 'delete'] : $only;                                                                                         
                                                                                                                                                                                   
          $map = [                                                                                                                                                                 
              'list' => $this->readStub('list.stub'),                                                                                                                              
              'detail' => $this->readStub('detail.stub'),                                                                                                                          
              'create' => $this->readStub('create.stub'),                                                                                                                          
              'update' => $this->readStub('update.stub'),                                                                                                                          
              'delete' => $this->readStub('delete.stub'),                                                                                                                          
          ];                                                                                                                                                                       
                                                                                                                                                                                   
          $methods = [];                                                                                                                                                           
                                                                                                                                                                                   
          foreach ($only as $item) {                                                                                                                                               
              if (! isset($map[$item])) {                                                                                                                                          
                  continue;                                                                                                                                                        
              }                                                                                                                                                                    
                                                                                                                                                                                   
              $methods[] = str_replace(                                                                                                                                            
                  ['{{RoutePrefix}}', '{{TagName}}'],                                                                                                                              
                  [$routePrefix, $tagName],                                                                                                                                        
                  $map[$item]                                                                                                                                                      
              );                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          return implode("\n\n", $methods);                                                                                                                                        
      }                                                                                                                                                                            
                                                                                                                                                                                   
      protected function readStub(string $name): string                                                                                                                            
      {                                                                                                                                                                            
          return file_get_contents($this->packageBasePath . '/stubs/' . $name);                                                                                                    
      }                                                                                                                                                                            
  }