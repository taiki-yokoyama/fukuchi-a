<?
class Baggage {
    private $id;
    private Item $item;
    private $isTemplate;

    public function __construct($id, Item $item, $isTemplate) {
        $this->id = $id;
        $this->item = $item;
        $this->isTemplate = $isTemplate;
    }
}