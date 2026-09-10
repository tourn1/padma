<?php
// Script temporal para hacer el reemplazo del bloque de imágenes
$file = '/Users/mauriciotourn/Documents/DESARROLLOS/PERSONAL/PADMA/padma/admin/products.php';
$content = file_get_contents($file);

$startMarker = '    $imageFilename = \'\';';
$endMarker = '    // Si no hay errores, guardar cambios';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $replacement = <<<'EOD'
    // Procesar imágenes existentes
    $imagenes = [];
    if ($action === 'edit' && $editProduct) {
        // Compatibilidad: si tiene 'imagen' pero no 'imagenes', convertir a array
        $existingImages = $editProduct['imagenes'] ?? [];
        if (empty($existingImages) && !empty($editProduct['imagen'])) {
            $existingImages[] = ['archivo' => $editProduct['imagen'], 'nombre' => 'Principal'];
        }

        // Obtener imágenes a eliminar y nombres
        $imagenesAEliminar = isset($_POST['eliminar_imagenes']) ? $_POST['eliminar_imagenes'] : [];
        $nombresExistentes = isset($_POST['nombres_imagenes_existentes']) ? $_POST['nombres_imagenes_existentes'] : [];

        foreach ($existingImages as $index => $img) {
            if (in_array((string)$index, $imagenesAEliminar, true)) {
                // Eliminar archivo
                if (file_exists($imgUploadDir . $img['archivo'])) {
                    @unlink($imgUploadDir . $img['archivo']);
                }
            } else {
                // Conservar y actualizar nombre si es necesario
                if (isset($nombresExistentes[$index])) {
                    $img['nombre'] = trim($nombresExistentes[$index]);
                }
                $imagenes[] = $img;
            }
        }
    }

    // Procesar subida de nuevas imágenes
    if (isset($_FILES['imagenes_nuevas'])) {
        $nombresNuevos = isset($_POST['nombres_imagenes_nuevas']) ? $_POST['nombres_imagenes_nuevas'] : [];
        
        // Cuando se usa input name="array[]", $_FILES es un array de arrays
        $fileCount = is_array($_FILES['imagenes_nuevas']['name']) ? count($_FILES['imagenes_nuevas']['name']) : 0;
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['imagenes_nuevas']['error'][$i] === UPLOAD_ERR_OK) {
                if (!is_writable($imgUploadDir)) {
                    $errorMessages[] = "Error de permisos: La carpeta de imágenes 'assets/img/catalogo/' no tiene permisos de escritura.";
                    break;
                }
                
                $tmpPath = $_FILES['imagenes_nuevas']['tmp_name'][$i];
                $fileExtension = pathinfo($_FILES['imagenes_nuevas']['name'][$i], PATHINFO_EXTENSION);
                
                $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nombre));
                $newFilename = $cleanName . '_' . time() . '_' . $i . '.' . $fileExtension;
                $targetPath = $imgUploadDir . $newFilename;
                
                $imageInfo = @getimagesize($tmpPath);
                if ($imageInfo !== false) {
                    if (move_uploaded_file($tmpPath, $targetPath)) {
                        $nombreImg = isset($nombresNuevos[$i]) && trim($nombresNuevos[$i]) !== '' ? trim($nombresNuevos[$i]) : 'Imagen ' . (count($imagenes) + 1);
                        $imagenes[] = [
                            'archivo' => $newFilename,
                            'nombre' => $nombreImg
                        ];
                    } else {
                        $errorMessages[] = "No se pudo guardar una de las imágenes subidas.";
                    }
                }
            } elseif ($_FILES['imagenes_nuevas']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $errorMessages[] = "Error al subir imagen (Código: " . $_FILES['imagenes_nuevas']['error'][$i] . ").";
            }
        }
    }

EOD;
    $newContent = substr($content, 0, $startPos) . $replacement . substr($content, $endPos);
    file_put_contents($file, $newContent);
    echo "Done updating image post logic.\n";
} else {
    echo "Could not find markers.\n";
}
?>
