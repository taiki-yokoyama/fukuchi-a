<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../Application/Usecase/ItemRegisterUsecase.php';
class ItemController {
    public ItemRegisterUsecase itemRegisterUsecase;
    public function ItemController (ItemRegisterUsecase $itemRegisterUsecase ) {
        $this->itemRegisterUsecase = $itemRegisterUsecase;
    }
    
    public function saveItem($name, $tagId) {
        $this->itemRegisterUsecase->registerItem($name, $tagId);
        header('Location: /items');
        exit();
    }
}