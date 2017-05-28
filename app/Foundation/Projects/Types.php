<?php

namespace Yap\Foundation\Projects;

use Yap\Auxiliary\ApiAdaptors\Taiga;
use Yap\Models\ProjectType;

class Types
{

    /**
     * @var \Yap\Models\ProjectType
     */
    private $type;

    /**
     * @var \Yap\Auxiliary\ApiAdaptors\Taiga
     */
    private $taiga;


    public function __construct(ProjectType $type, Taiga $taigaAdaptor)
    {

        $this->type  = $type;
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