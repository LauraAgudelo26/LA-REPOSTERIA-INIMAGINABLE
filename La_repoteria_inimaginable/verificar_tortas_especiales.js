import connection from './src/config/db.js';

async function verificarTortasEspeciales() {
    try {
        console.log('=== VERIFICANDO CATEGORÍAS ===');
        const [categorias] = await connection.query('SELECT * FROM categoria');
        console.log('Categorías:', categorias);
        
        console.log('\n=== VERIFICANDO PRODUCTOS POR CATEGORÍA ===');
        const [productos] = await connection.query(`
            SELECT p.id, p.nombre, p.categoria_id, c.nombre as categoria_nombre, p.activo
            FROM producto p
            LEFT JOIN categoria c ON p.categoria_id = c.id
            ORDER BY p.categoria_id, p.nombre
        `);
        
        console.log('\nTodos los productos:');
        productos.forEach(p => {
            console.log(`  - ${p.nombre} (ID: ${p.id}, Categoría: ${p.categoria_id} - ${p.categoria_nombre}, Activo: ${p.activo})`);
        });
        
        console.log('\n=== PRODUCTOS DE TORTAS ESPECIALES (Categoría 2) ===');
        const [tortasEspeciales] = await connection.query(`
            SELECT p.*, c.nombre as categoria_nombre
            FROM producto p
            INNER JOIN categoria c ON p.categoria_id = c.id
            WHERE p.categoria_id = 2 AND p.activo = 1
        `);
        
        if (tortasEspeciales.length === 0) {
            console.log('❌ NO HAY PRODUCTOS en categoría 2 (Tortas Especiales)');
        } else {
            console.log(`✅ Se encontraron ${tortasEspeciales.length} productos:`);
            tortasEspeciales.forEach(p => {
                console.log(`  - ${p.nombre} (Stock: ${p.stock}, Precio: $${p.precio})`);
            });
        }
        
        await connection.end();
    } catch (error) {
        console.error('Error:', error);
        process.exit(1);
    }
}

verificarTortasEspeciales();
