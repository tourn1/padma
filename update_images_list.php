<?php
$file = '/Users/mauriciotourn/Documents/DESARROLLOS/PERSONAL/PADMA/padma/admin/products.php';
$content = file_get_contents($file);

$startMarker = '                                        <?php if (!empty($p[\'imagen\']) && file_exists($imgUploadDir . $p[\'imagen\'])): ?>';
$endMarker = '                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; font-weight: bold; color: #718096;"><?php echo e($p[\'orden\'] ?? 0); ?></td>';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $replacement = <<<'EOD'
                                        <?php 
                                        $mainImage = '';
                                        if (!empty($p['imagenes']) && isset($p['imagenes'][0]['archivo']) && file_exists($imgUploadDir . $p['imagenes'][0]['archivo'])) {
                                            $mainImage = $p['imagenes'][0]['archivo'];
                                        } elseif (!empty($p['imagen']) && file_exists($imgUploadDir . $p['imagen'])) {
                                            $mainImage = $p['imagen'];
                                        }
                                        ?>
                                        <?php if ($mainImage !== ''): ?>
                                            <img src="../assets/img/catalogo/<?php echo e($mainImage); ?>" class="product-img-thumb" alt="<?php echo e($p['nombre']); ?>" style="cursor: pointer;" onclick="openImageModal(this.src)">
                                            <?php if (!empty($p['imagenes']) && count($p['imagenes']) > 1): ?>
                                                <div style="font-size: 0.75rem; text-align: center; color: #718096; margin-top: 5px;">+<?php echo (count($p['imagenes']) - 1); ?> imgs</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="product-img-thumb" style="display: flex; align-items: center; justify-content: center; color: #a0aec0;">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
EOD;
    $newContent = substr($content, 0, $startPos) . $replacement . substr($content, $endPos);
    file_put_contents($file, $newContent);
    echo "Done updating list view.\n";
} else {
    echo "Markers not found.\n";
}
?>
