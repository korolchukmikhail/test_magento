<?php

class ROI_Cin7_Model_Api_Product_Option_Import extends ROI_Cin7_Model_Api_Abstract
{
    public function getAllOptions()
    {
        $page = 1;
        $productOptions = [];
        $rows = $this->_helper->getCountRows();
        $tempDir = $this->_helper->getTempDir();

        // TODO: remove from production!
        if (file_exists($tempDir . '/productOptions.json')) {
            return json_decode(file_get_contents($tempDir . '/productOptions.json'), true);
        }
        // TODO: remove from production!

        try {
            while (true) {
                $path = "/Products?fields=id,optionLabel1,optionLabel2,optionLabel3,productOptions&page={$page}&rows={$rows}";

                $data = $this->getData($path);

                if (empty($data)) {
                    break;
                }

                if (is_array($data)) {
                    $productOptions = array_merge($productOptions, $data);
                } else {
                    break;
                }

                $page++;
            }

        } catch (Exception $e) {
            print_r($e->getMessage() . "\n");
        }

        $productOptions = $this->prepareOptions($productOptions);
        if ($productOptions) {
            file_put_contents($tempDir . '/productOptions.json', json_encode($productOptions));
        }

        return $productOptions;
    }

    private function prepareOptions($rawOptions)
    {
        $options = [];
        foreach ($rawOptions as $option) {
            if (!$option['optionLabel1'] && !$option['optionLabel2'] && !$option['optionLabel3']) {
                continue;
            }

            $options[] = $option;
        }

        $optionWithValues = [];

        foreach ($options as $option) {
            if (strlen($option['optionLabel1']) > 0 && !isset($optionWithValues[$option['optionLabel1']])) {
                $optionWithValues[$option['optionLabel1']]['values'] = [];
            }
            if (strlen($option['optionLabel2']) > 0 && !isset($optionWithValues[$option['optionLabel2']])) {
                $optionWithValues[$option['optionLabel2']]['values'] = [];
            }
            if (strlen($option['optionLabel3']) > 0 && !isset($optionWithValues[$option['optionLabel3']])) {
                $optionWithValues[$option['optionLabel3']]['values'] = [];
            }

            if (isset($option['productOptions']) && $option['productOptions']) {
                foreach ($option['productOptions'] as $item) {
                    if (strlen($item['option1']) > 0) {
                        $optionWithValues[$option['optionLabel1']]['values'][] = $item['option1'];
                    }

                    if (strlen($item['option2']) > 0) {
                        $optionWithValues[$option['optionLabel2']]['values'][] = $item['option2'];
                    }

                    if (strlen($item['option3']) > 0) {
                        $optionWithValues[$option['optionLabel3']]['values'][] = $item['option3'];
                    }
                }
            }
        }

        foreach ($optionWithValues as $key => &$option) {
            if (count($option['values']) > 0) {
                $values = array_unique($option['values']);
                sort($values, SORT_NATURAL | SORT_FLAG_CASE);
                $optionWithValues[$key]['values'] = $values;
            } else {
                unset($optionWithValues[$key]);
            }
        }

        ksort($optionWithValues, SORT_NATURAL | SORT_FLAG_CASE);

        return $optionWithValues;
    }
}