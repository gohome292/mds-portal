<?php
class TransactionBehavior extends ModelBehavior
{
    // @param object $model
    // @return boolean
    function begin(&$model)
    {
        return $model->getDataSource()->begin($model);
    }
    
    // @param object $model
    // @return boolean
    function commit(&$model)
    {
        return $model->getDataSource()->commit($model);
    }
    
    // @param object $model
    // @return boolean
    function rollback(&$model)
    {
        return $model->getDataSource()->rollback($model);
    }
}
