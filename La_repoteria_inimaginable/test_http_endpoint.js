// Test del endpoint HTTP
const testEndpoint = async () => {
    try {
        console.log('🧪 Testeando: http://localhost:3000/api/productos/categoria/2\n');
        
        const response = await fetch('http://localhost:3000/api/productos/categoria/2');
        const data = await response.json();
        
        console.log('✅ Respuesta del servidor:');
        console.log(`   - Status: ${response.status}`);
        console.log(`   - Success: ${data.success}`);
        console.log(`   - Productos: ${data.data.length}`);
        
        if (data.data.length > 0) {
            console.log('\n📦 Productos recibidos:');
            data.data.forEach(p => {
                console.log(`   - ${p.nombre} ($${p.precio})`);
            });
        } else {
            console.log('\n❌ No se recibieron productos');
        }
        
    } catch (error) {
        console.error('❌ Error:', error.message);
    }
};

// Esperar que el servidor esté listo
setTimeout(testEndpoint, 2000);
