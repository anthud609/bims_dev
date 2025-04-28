<?php
// core/Stub.php
namespace Core;

class Stub
{
    protected string $template;

    public function __construct(string $pathToStub)
    {
        $this->template = file_get_contents($pathToStub);
    }

    public function render(array $vars): string
    {
        $output = $this->template;
        foreach ($vars as $key => $val) {
            $output = str_replace("{{{$key}}}", $val, $output);
        }
        return $output;
    }
}
