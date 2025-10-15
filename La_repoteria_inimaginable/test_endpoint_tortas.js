import connection from './src/config/db.js';
import ProductoModel from './src/models/producto_model.js';

async function testearEndpoint() {
    try {
        console.log('=== TESTEANDO ENDPOINT DE TORTAS ESPECIALES ===\n');
        
        const productos = await ProductoModel.getByCategoria(2);
        
        console.log(`✅ Se obtuvieron ${productos.length} productos de la categoría 2:`);
        
        productos.forEach(p => {
            console.log(`\n📦 ${p.nombre}`);
            console.log(`   - ID: ${p.id}`);
            console.log(`   - Categoría ID: ${p.categoria_id}`);
            console.log(`   - Categoría: ${p.categoria_nombre}`);
            console.log(`   - Precio: $${parseFloat(p.precio).toLocaleString('es-CO')}`);
            console.log(`   - Stock: ${p.stock} unidades`);
            console.log(`   - Imagen: ${p.imagen}`);
            console.log(`   - Destacado: ${p.destacado ? 'Sí' : 'No'}`);
            console.log(`   - Activo: ${p.activo ? 'Sí' : 'No'}`);
        });
        
        await connection.end();
    } catch (error) {
        console.error('❌ Error:', error);
        process.exit(1);
    }
}

testearEndpoint();
