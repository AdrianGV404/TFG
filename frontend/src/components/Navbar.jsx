import { Link } from "react-router-dom";

function Navbar() {
  const navStyle = {
    position: "fixed",
    top: 0,
    left: 0,
    right: 0,
    height: "40px",
    backgroundColor: "#222",
    display: "flex",
    alignItems: "center",
    padding: "0 20px",
    gap: "20px",
    zIndex: 1000,
  };

  const linkStyle = {
    color: "white",
    textDecoration: "none",
    fontWeight: "bold",
    lineHeight: "40px",
  };

  return (
    <nav style={navStyle}>
      <Link to="/" style={linkStyle}>
        Inicio
      </Link>
      <Link to="/search" style={linkStyle}>
        Buscar y Filtrar
      </Link>
      <Link to="/correlation" style={linkStyle}>
        Análisis de Correlación
      </Link>
      <Link to="/prediction" style={linkStyle}>
        Predicciones
      </Link>
      <Link to="/public-spending" style={linkStyle}>
        Gasto Público
      </Link>
      <Link to="/export" style={linkStyle}>
        Exportar Informes
      </Link>
    </nav>
  );
}

export default Navbar;
