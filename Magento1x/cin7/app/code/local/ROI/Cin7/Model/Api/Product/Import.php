<?php

class ROI_Cin7_Model_Api_Product_Import extends ROI_Cin7_Model_Api_Abstract
{
    public function importAllProducts($ids = [])
    {
        $page = 1;
        $products = [];
        $rows = $this->_helper->getCountRows();
        $tempDir = $this->_helper->getTempDir();

        // TODO: remove from production!
        if (file_exists($tempDir . '/products.json') && empty($ids)) {
            return json_decode(file_get_contents($tempDir . '/products.json'), true);
        }
        // TODO: remove from production!

        try {
            if (empty($ids)) {
                while (true) {
                    $path = "/Products?page={$page}&rows={$rows}";
                    $data = $this->getData($path);

                    if (empty($data)) {
                        break;
                    }

                    if (is_array($data)) {
                        $products = array_merge($products, $data);
                    } else {
                        break;
                    }

                    $page++;
                }
                if ($products) {
                    file_put_contents($tempDir . '/products.json', json_encode($products));
                }
            } else {
                foreach ($ids as $id) {
                    $data = $this->getData("/Products/{$id}");
                    if ($data) {
                        $products = array_merge($products, [$data]);
                    }
                }
            }

        } catch (Exception $e) {
            print_r($e->getMessage() . "\n");
        }

        return $products;
    }
}