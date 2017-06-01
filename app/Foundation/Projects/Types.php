<?php

namespace Yap\Foundation\Projects;

use Yap\Auxiliary\ApiAdaptors\Taiga;
use Yap\Exceptions\TaigaOfflineException;
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

    /**
     * @var \Yap\Auxiliary\HttpCheckers\Taiga
     */
    protected $checker;


    public function __construct(ProjectType $type, Taiga $taigaAdaptor, \Yap\Auxiliary\HttpCheckers\Taiga $checker)
    {

        $this->type  = $type;
        $this->taiga = $taigaAdaptor;
        $this->checker = $checker;
    }


    public function installOrUpdate()
    {
        $this->checker->checkAndThrow();

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