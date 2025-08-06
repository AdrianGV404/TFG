import { Outlet } from "react-router-dom";

function Layout() {
  return (
    <>
      <main style={{ paddingTop: "40px", padding: "20px" }}>
        <Outlet />
      </main>
    </>
  );
}

export default Layout;
