<?php
include_once("db/AccesoDatos.php");

class Reportes
{
    
    public static function PedidoProductoListoParaServir()
    {
        $sql = "SELECT p.id_mesa as mesa, 
                       pr.nombre as producto, 
                       pp.cantidad as cantidad
                FROM producto pr 
                    right JOIN pedido_producto pp ON pp.id_producto = pr.id 
                    left JOIN pedido p ON pp.id_pedido = p.id 
                WHERE pp.estado = 2 AND pp.activo = 1;";
        
        return AccesoDatos::ObtenerConsulta($sql, null);
    }
    // 4 - El cliente
    public static function DemoraPedidoMesa($mesa, $pedido)
    {
        $pedidoAux = str_replace($pedido[0],'1', $pedido); 
        $mesaAux = str_replace($mesa[0],'1', $mesa); 

        $sql= "SELECT 
                TIMESTAMPDIFF(minute, DATE_FORMAT(p.created_at, '%Y%m%d%H%i%s'), 
                                      DATE_FORMAT(p.fecha_prevista, '%Y%m%d%H%i%s')) 
                                      as 'Espera total (en min)',
                TIMESTAMPDIFF(minute, DATE_FORMAT(NOW(), '%Y%m%d%H%i%s'),
                                      DATE_FORMAT(p.fecha_prevista, '%Y%m%d%H%i%s')) 
                                      as 'Quedan (en min)'
              FROM pedido p
              WHERE p.id_mesa = $mesaAux AND p.id = $pedidoAux AND p.fecha_fin is null AND activo = 1;";
    
        return AccesoDatos::ObtenerConsulta($sql, null);
    }

    // 5 - Solo socios

    public static function DemoraPedidosCerrados()
    {
        $sql = "SELECT 
                    p.id as 'Num. Pedido',
                    p.id_mesa as 'Mesa',
                    CONCAT
                    (
                    MOD (TIMESTAMPDIFF(hour, DATE_FORMAT(p.created_at, '%Y%m%d%H%i%s'), DATE_FORMAT(p.fecha_fin, '%Y%m%d%H%i%s')), 24),' h ',
                    MOD (TIMESTAMPDIFF(minute, DATE_FORMAT(p.created_at, '%Y%m%d%H%i%s'), DATE_FORMAT(p.fecha_fin, '%Y%m%d%H%i%s')), 60), 'min') 
                    as 'Demora'                      
                FROM pedido p
                WHERE p.estado = 4 AND p.activo = 1; ";

        return AccesoDatos::ObtenerConsulta($sql, null);
    }

    // 8 - Solo socios

    public static function EstadoMesas()
    {
        $sql = "SELECT 
                    CONCAT(p.id_mesa, ' - ', m.nombre) as 'Mesa',
                    p.id as 'Num. Pedido',
                    CASE 
                    WHEN p.estado = 1 THEN 'Cliente esperando'
                    WHEN p.estado = 2 THEN 'Cliente comiendo'
                    WHEN p.estado = 3 THEN 'Cliente pagando'
                    ELSE 'ERROR' END
                    as Estado
                FROM pedido p LEFT JOIN mesa m ON p.id_mesa = m.id
                WHERE m.activo = 1 AND p.activo = 1 AND p.estado < 4
                ORDER BY m.id; ";
        
        return AccesoDatos::ObtenerConsulta($sql, null);

    }

    // 12 - Solo socios

    public static function MejoresComentarios()
    {
        $sql = "SELECT 
                    ((e.nota_restaurante + e.nota_mozo + e.nota_cocinero)  / 3)  as 'Promedio',
                    e.texto as 'Comentario'
                FROM encuesta e
                WHERE e.activo = 1 
                ORDER BY ((e.nota_restaurante + e.nota_mozo + e.nota_cocinero)  / 3)  DESC
                LIMIT 5;";
        
        return AccesoDatos::ObtenerConsulta($sql, null);

    }

    // 13 - Solo socios

    public static function MesaMasUsada()
    {
        $sql = "SELECT CONCAT(p.id_mesa, ' - ', m.nombre) as 'Mesa', count(p.id_mesa) Uso
                FROM pedido p LEFT JOIN mesa m ON p.id_mesa = m.id
                WHERE m.activo = 1 AND p.activo = 1
                GROUP BY p.id_mesa
                HAVING COUNT(p.id_mesa) = Uso;";
        
        return AccesoDatos::ObtenerConsulta($sql, null);
    }

    public static function Cuenta($idPedido)
    {
        $sql = "SELECT pr.nombre as producto, 
                        pp.cantidad as cantidad
                FROM producto pr 
                        right JOIN pedido_producto pp ON pp.id_producto = pr.id 
                        left JOIN pedido p ON pp.id_pedido = p.id 
                WHERE pp.estado = 2 AND pp.activo = 1 AND p.id = $idPedido;";
        
        return AccesoDatos::ObtenerConsulta($sql, null);
    }
}
?>