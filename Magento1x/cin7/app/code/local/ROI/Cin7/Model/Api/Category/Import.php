<?php

class ROI_Cin7_Model_Api_Category_Import extends ROI_Cin7_Model_Api_Abstract
{
    public function getAllCategories()
    {
        $page = 1;
        $categories = [];
        $rows = $this->_helper->getCountRows();
        $tempDir = $this->_helper->getTempDir();

        // TODO: remove from production!
        if (file_exists($tempDir . '/categories.json')) {
            return json_decode(file_get_contents($tempDir . '/categories.json'), true);
        }
        // TODO: remove from production!

        try {
            while (true) {
                $path = "/ProductCategories?page={$page}&rows={$rows}";
                $data = $this->getData($path);

                if (empty($data)) {
                    break;
                }

                if (is_array($data)) {
                    $categories = array_merge($categories, $data);
                } else {
                    break;
                }

                $page++;
            }

        } catch (Exception $e) {
            print_r($e->getMessage() . "\n");
        }

        $categories = $this->prepareCategories($categories);
        if ($categories) {
            file_put_contents($tempDir . '/categories.json', json_encode($categories));
        }

        return $categories;
    }

    private function prepareCategories($categories)
    {
        return $categories;
    }
}