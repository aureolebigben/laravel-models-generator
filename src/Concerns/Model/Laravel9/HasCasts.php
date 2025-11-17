<?php

declare(strict_types=1);

namespace GiacomoMasseroni\LaravelModelsGenerator\Concerns\Model\Laravel9;

use GiacomoMasseroni\LaravelModelsGenerator\Writers\Model\Writer;

/**
 * @mixin Writer
 */
trait HasCasts
{
    public function casts(): string
    {
        $body = '';

        if (count($this->entity->casts) > 0) {
            $body .= $this->spacer.'/**'."\n";
            $body .= $this->spacer.' * @var array<string, string>'."\n";
            $body .= $this->spacer.' */'."\n";
            $body .= $this->spacer.'protected $casts = ['."\n";
            foreach ($this->entity->casts as $column => $type) {
                $configEnums = (array) config('models-generator.enums_casting', []);
                if (array_key_exists($this->entity->name, $configEnums)
                    && array_key_exists($column, $configEnums[$this->entity->name])
                ) {
                    $type = '\\'.$configEnums[$this->entity->name][$column].'::class';
                } elseif (array_key_exists($column, $configEnums)) {
                    $type = '\\'.$configEnums[$column].'::class';
                } else {
                    $type = '\''.$type.'\'';
                }
                $body .= str_repeat($this->spacer, 2).'\''.$column.'\' => '.$type.','."\n";
            }
            $body .= str_repeat($this->spacer, 1).'];';

            return $body;
        }

        return '';
    }
}
