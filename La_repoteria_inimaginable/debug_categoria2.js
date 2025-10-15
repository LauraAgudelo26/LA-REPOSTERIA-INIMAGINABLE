import connection from './src/config/db.js';

async function debugCategoria2() {
    try {
        console.log('=== DEBUG: Productos Categoría 2 ===\n');
        
        // Query directo igual al del modelo
        const [rows] = await connection.query(`
            SELECT 
                p.id,
                p.nombre,
                p.descripcion,
                p.precio,
                p.stock,
                p.imagen,
                p.destacado,
                p.activo,
                c.id as categoria_id,
                c.nombre as categoria_nombre
            FROM producto p
            INNER JOIN categoria c ON p.categoria_id = c.id
            WHERE p.categoria_id = ? AND p.activo = 1
            ORDER BY p.nombre ASC
        `, [2]);
        
        console.log(`✅ Query ejecutado. Filas retornadas: ${rows.length}\n`);
        
        if (rows.length === 0) {
            console.log('❌ NO SE ENCONTRARON PRODUCTOS\n');
            
            // Verificar sin el filtro activo = 1
            const [allRows] = await connection.query(`
                SELECT p.*, c.nombre as categoria_nombre
                FROM producto p
                INNER JOIN categoria c ON p.categoria_id = c.id
                WHERE p.categoria_id = 2
            `);
            
            console.log(`Sin filtro activo=1: ${allRows.length} productos`);
            allRows.forEach(p => {
                console.log(`  - ${p.nombre} (activo: ${p.activo})`);
            });
        } else {
            console.log('Productos encontrados:');
            rows.forEach(p => {
                console.log(`\n  ${p.nombre}`);
                console.log(`    ID: ${p.id}`);
                console.log(`    Categoría: ${p.categoria_id} - ${p.categoria_nombre}`);
                console.log(`    Precio: $${p.precio}`);
                console.log(`    Stock: ${p.stock}`);
                console.log(`    Imagen: ${p.imagen}`);
                console.log(`    Activo: ${p.activo}`);
            });
        }
        
        await connection.end();
    } catch (error) {
        console.error('Error:', error);
        process.exit(1);
    }
}

debugCategoria2();
