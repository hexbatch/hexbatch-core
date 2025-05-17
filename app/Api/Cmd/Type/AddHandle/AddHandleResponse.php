<?php
namespace App\Api\Cmd\Type\AddHandle;

use App\Api\Cmd\Design\Promote\DesignPromoteResponse;
use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\ElementType;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypeHandleAdd;

class AddHandleResponse extends TypeHandleAdd implements IActionWorkReturn,IActionOaResponse,IActionWorker
{

    public function __construct(
        /** @var ElementType[] $edited_types */
        protected array $edited_types = []
    )
    {
        parent::__construct();
    }

    public function toThing( $thing)
    {

    }

    protected function run(AddHandleParams $params) {

        $edited_types = ElementType::whereIn('id',$params->getTypeIds())->get();
        $this->edited_types = [];
        /** @var ElementType $et */
        foreach ($edited_types as $et) {
            if ($params->getHandleElementId()) {
                $et->type_handle_element_id = $params->getHandleElementId();
            } else {
                $et->type_handle_element_id = null;
            }
            $et->save();
        }

    }

    /**
     * @param AddHandleParams $params
     * @return DesignPromoteResponse
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,AddHandleParams::class) || is_subclass_of($params,AddHandleParams::class))) {
            throw new HexbatchInvalidException("Params is not AddHandleParams");
        }
        $worker = new AddHandleResponse();
        $worker->run($params);
        return $worker;
    }

    /**
     * @return ElementType[]
     */
    public function getEditedTypes(): array
    {
        return $this->edited_types;
    }


}
