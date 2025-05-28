<?php
function buildTree(array &$elements, $parentId = null) {
    $branch = [];

    foreach ($elements as &$element) {
        if ($element['ParentId'] == $parentId) {
            $children = buildTree($elements, $element['Id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
            unset($element);
        }
    }
    return $branch;
}
?>
