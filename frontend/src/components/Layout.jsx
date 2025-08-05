import { Outlet } from "react-router-dom";
import Navbar from "./Navbar";

function Layout() {
  return (
    <>
      <Navbar />
      {/* Espacio para navbar fijo */}
      <main style={{ paddingTop: "40px", padding: "20px" }}>
        <Outlet />
      </main>
    </>
  );
}

export default Layout;
