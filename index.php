<?php

class TelegraphText
{
    public $title, $text, $author, $published, $slug;
    public function __construct($author, $slug)
    {
        $this->author = $author;
        $this->slug = $slug;
        $this->published = date('d/m/y/h/i');
    }
    public function storeText()
    {
        $arrayText['title'] = $this->title;
        $arrayText['text'] = $this->text;
        $arrayText['author'] = $this->author;
        $arrayText['published'] = $this->published;
        file_put_contents($this->slug, serialize($arrayText));
    }
    public function loadText()
    {
        if (file_exists($this->slug)) {
            $arrayText = unserialize(file_get_contents($this->slug));
            $this->title = $arrayText['title'];
            $this->text = $arrayText['text'];
            $this->author = $arrayText['author'];
            $this->published = $arrayText['published'];
            return ($this->text);
        }
    }
    public function editText($title, $text)
    {
        $this->title = $title;
        $this->text = $text;
    }
}
abstract class Storage
{
    abstract function create($object);
    abstract function read($slug);
    abstract function update($item, $slug);
    abstract function delete($slug);
    abstract function list();
}
abstract class View
{
    public $storage;
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }
    abstract function displayTextById($id);
    abstract function displayTextByUrl($url);
}
abstract class User
{
    public $id, $name, $role;
    abstract function getTextsToEdit();
}
class FileStorage extends Storage
{
    public $directory = __DIR__;
    public function create($object)
    {
        $fileName = $object->slug . '_' . date('d_m_Y') . '.txt';
        $object->slug = $fileName;
        $i = 1;
        while (file_exists($object->slug)) {
            $object->slug = $fileName . '_' . $i++ . '.txt';
        }
        file_put_contents($object->slug, serialize($object));
        return $object->slug;
    }
    public function read($slug)
    {
        $read = file_get_contents($slug);
        if (strlen($read) > 0) {
            $text = unserialize($read);
            return $text;
        }
        return false;
    }
    public function update($item, $slug)
    {
        $update = file_get_contents($slug);
        if (strlen($update) > 0) {
            $text = unserialize($update);
            file_put_contents($text, $item);
            $text = serialize($text);
            return $text;
        }
        return false;
    }
    public function delete($slug)
    {
        $delete = file_get_contents($slug);
        if (strlen($delete) > 0) {
            unlink($slug);
        }
        return false;
    }
    public function list()
    {
        $search = scandir($this->directory);
    }
}

$telegraphObj = new TelegraphText("Роман Пономарев", "test_text_file.txt");

$files = new FileStorage();

$telegraphObj->text = "Text";
$telegraphObj->title = 'Title';

$files->create($telegraphObj);

var_dump($telegraphObj->slug);
var_dump($files->read($telegraphObj->slug));
