import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import SearchAndFilter from "./pages/SearchAndFilter";
import CorrelationAnalysis from "./pages/CorrelationAnalysis";
import Prediction from "./pages/Prediction";
import PublicSpending from "./pages/PublicSpending";
import ExportReports from "./pages/ExportReports";

import Layout from "./components/Layout";

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route index element={<Home />} />
          <Route path="search" element={<SearchAndFilter />} />
          <Route path="correlation" element={<CorrelationAnalysis />} />
          <Route path="prediction" element={<Prediction />} />
          <Route path="public-spending" element={<PublicSpending />} />
          <Route path="export" element={<ExportReports />} />
        </Route>
      </Routes>
    </Router>
  );
}

export default App;
