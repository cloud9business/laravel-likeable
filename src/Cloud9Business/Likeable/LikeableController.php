<?php namespace Cloud9Business\Likeable;

class LikeableController extends \Controller
{

    public function like()
    {

        $likeableEntities = \Config::get('likeable::entities');

        $validator = $this->getValidator($likeableEntities);

        if ($validator->passes()) {

            /** @var \Eloquent $entityClass */
            $entityClass = $likeableEntities[\Input::get('entityName')];

            /** @var LikeableTrait $entity */
            $entity = $entityClass::find(\Input::get('id'));

            switch (\Input::get('type')) {
                case 'like':
                    $entity->like();
                    break;
                case 'unlike':
                    $entity->unlike();
                    break;
            }

            return array(
                'success' => true
            );
        } else {
            return array(
                'success' => false,
                'errors' => $validator->errors(),
            );
        }
    }

    /**
     * @param array $likeableEntities
     * @return mixed
     */
    protected function getValidator($likeableEntities)
    {
        $rules = array(
            'type' => array('required', 'in:like,unlike'),
            'entityName' => array('required', 'in:' . implode(',', array_keys($likeableEntities))),
            'id' => 'required|numeric',
        );

        /** @var \Eloquent|bool $entityClass */
        if (array_key_exists(\Input::get('entityName'), $likeableEntities)) {
            $entityClass = $likeableEntities[\Input::get('entityName')];
        } else {
            $entityClass = false;
        }

        if (!$entityClass || !class_exists($entityClass)) {
            $rules['entityClass'] = 'required'; // fake rule for not exist field
        } else {
            $tableName = with(new $entityClass)->getTable();
            $rules['id'] .= '|exists:' . $tableName . ',id';
        }

        return \Validator::make(\Input::all(), $rules);
    }
}
