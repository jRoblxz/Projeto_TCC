import React, { FC } from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";
import { Toaster } from "react-hot-toast";

// Páginas
import Login from "../pages/Login";
import Trilha from "../pages/Trilha";

// Componentes
import ProtectedRoute from "../components/ProtectedRoute";

import "./App.css";

// Tipagem opcional para os filhos do ProtectedRoute
interface ProtectedRouteProps {
  children: React.ReactNode;
}

// Se quiser tipar o ProtectedRoute aqui (caso não tenha)
const TypedProtectedRoute: FC<ProtectedRouteProps> = ({ children }) => {
  return <ProtectedRoute>{children}</ProtectedRoute>;
};

const App: FC = () => {
  return (
    <Router>
      <div className="App">
        <Toaster
          position="top-right"
          toastOptions={{
            duration: 3000,
            style: {
              background: "#363636",
              color: "#fff",
            },
          }}
        />

        <Routes>
          {/* Rota pública */}
          <Route path="/login" element={<Login />} />

           <Route
            path="/Trilha"
            element={
              <ProtectedRoute>
                <Trilha />
              </ProtectedRoute>
            }      
          />

          {/* Redirecionamentos */}
          <Route path="/" element={<Navigate to="/login" replace />} />
          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </div>
    </Router>
  );
};

export default App;
