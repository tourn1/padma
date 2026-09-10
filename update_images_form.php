<?php
$file = '/Users/mauriciotourn/Documents/DESARROLLOS/PERSONAL/PADMA/padma/admin/products.php';
$content = file_get_contents($file);

$startMarker = '                    <div class="form-group">
                        <label class="form-label" for="imagen">Imagen del Producto (Dejar en blanco para mantener la actual)</label>';
$endMarker = '                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="activo"';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $replacement = <<<'EOD'
                    <div class="form-group">
                        <label class="form-label">Imágenes del Producto</label>
                        
                        <!-- Imágenes existentes -->
                        <?php 
                        $existingImages = $editProduct['imagenes'] ?? [];
                        if (empty($existingImages) && !empty($editProduct['imagen'])) {
                            $existingImages[] = ['archivo' => $editProduct['imagen'], 'nombre' => 'Principal'];
                        }
                        if (!empty($existingImages)): 
                        ?>
                            <div style="margin-bottom: 15px; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px;">
                                <span style="font-size: 0.85rem; color: #718096; display: block; margin-bottom: 10px;">Imágenes actuales:</span>
                                <?php foreach ($existingImages as $index => $img): ?>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; background: #f7fafc; padding: 10px; border-radius: 6px;">
                                        <img src="../assets/img/catalogo/<?php echo e($img['archivo']); ?>" class="product-img-thumb" style="width: 50px; height: 50px; min-width: 50px; object-fit: cover;" alt="Vista previa">
                                        <div style="flex: 1;">
                                            <input type="text" name="nombres_imagenes_existentes[<?php echo $index; ?>]" class="form-input" style="padding: 5px; font-size: 0.9rem;" value="<?php echo e($img['nombre'] ?? ''); ?>" placeholder="Nombre de imagen">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.85rem; color: #e53e3e; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                                <input type="checkbox" name="eliminar_imagenes[]" value="<?php echo $index; ?>"> Eliminar
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Nuevas imágenes -->
                        <div id="nuevas-imagenes-container">
                            <div class="nueva-imagen-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <input type="file" name="imagenes_nuevas[]" class="form-input" accept="image/*" style="flex: 1;">
                                <input type="text" name="nombres_imagenes_nuevas[]" class="form-input" style="flex: 1;" placeholder="Nombre (ej. Frente, Dorso)">
                            </div>
                        </div>
                        <button type="button" class="btn-action btn-secondary" style="font-size: 0.85rem; padding: 5px 10px;" onclick="agregarFilaImagen()">
                            <i class="fa-solid fa-plus"></i> Añadir otra imagen
                        </button>
                    </div>

                    <script>
                        function agregarFilaImagen() {
                            const container = document.getElementById('nuevas-imagenes-container');
                            const row = document.createElement('div');
                            row.className = 'nueva-imagen-row';
                            row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
                            row.innerHTML = `
                                <input type="file" name="imagenes_nuevas[]" class="form-input" accept="image/*" style="flex: 1;">
                                <input type="text" name="nombres_imagenes_nuevas[]" class="form-input" style="flex: 1;" placeholder="Nombre (ej. Frente, Dorso)">
                                <button type="button" class="btn-action btn-secondary" style="padding: 0 10px; color: #e53e3e; border-color: #fc8181; background: transparent;" onclick="this.parentElement.remove()">X</button>
                            `;
                            container.appendChild(row);
                        }
                    </script>

EOD;
    $newContent = substr($content, 0, $startPos) . $replacement . substr($content, $endPos);
    file_put_contents($file, $newContent);
    echo "Done updating HTML form.\n";
} else {
    echo "Markers not found.\n";
}
?>
