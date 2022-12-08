<?php

enum TipoEmpleado: int
{
    case BARTENDER = 1;
    case CERVECERO = 2;
    case COCINERO = 3;
    case MOZO = 4;
    case SOCIO = 5;
}

enum Sector: int
{
    case BARRA = 1;
    case CHOPERA = 2;
    case COCINA = 3;
    case CANDYBAR = 4;
}

enum EstadoMesa: int
{
    case CLIENTE_ESPERADO_PEDIDO = 1;
    case CLIENTE_COMIENDO = 2;
    case CLIENTE_PAGANDO = 3;
    case CERRADA = 4;
    case LIBRE = 5;

}

enum EstadoPedido: int
{
    case PENDIENTE = 1;
    case EN_PREPARACION = 2;
    case LISTO = 3;
    case CANCELADO = 4;
    case CERRADO = 5;
}

enum EstadoComanda: int
{
    case PENDIENTE = 1;
    case EN_PREPARACION = 2;
    case LISTO = 3;
}

enum AltaBaja : int
{
    case ALTA = 1;
    case BAJA = 0;
}

enum HttpCode : int
{
    case OK = 200;
    case CREATED = 201;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;
}
?>