<?php

namespace Yap\Foundation\Projects;

use Yap\Auxiliary\TaigaApi;
use Yap\Models\ProjectType;

class Types
{

    /**
     * @var \Yap\Models\ProjectType
     */
    private $type;

    /**
     * @var \Yap\Auxiliary\TaigaApi
     */
    private $taiga;


    public function __construct(ProjectType $type, TaigaApi $taiga)
    {

        $this->type = $type;
        $this->taiga = $taiga;
    }


    public function installOrUpdate()
    {
        $types = $this->taiga->getTypes();

        foreach ($types as $type) {
            $this->type->updateOrCreate([
                'taiga_id'    => $type->id,
                'name'        => $type->name,
                'description' => $type->description,
            ]);
        }
    }
}