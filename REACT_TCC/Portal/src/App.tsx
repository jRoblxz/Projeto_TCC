import React, { FC } from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";
import { Toaster } from "react-hot-toast";

// Páginas
import Login from "./pages/Login";
import Dashboard from "./pages/Dashboard";
import Peneira from "./pages/Peneiras";
import Players from "./pages/Players";

// Componentes
import ProtectedRoute from "./components/ProtectedRoute";

import "./App.css";
import PlayerInfo from "./pages/PlayerInfo";
import PlayerEdit from "./pages/PlayerEdit";
import PeneiraDetalhes from "./pages/PeneiraDetalhes";
import EditorTimes from "./pages/EditorTimes";

const App: FC = () => {
  return (
    <Router>
      <div className="App font-Jersey">
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
          {/* Rota Pública */}
          <Route path="/login" element={<Login />} />

          {/* Rota Protegida */}
          <Route
            path="/dashboard"
            element={
              <ProtectedRoute>
                <Dashboard />
              </ProtectedRoute>
            }
          />

          <Route
            path="/peneiras"
            element={
              <ProtectedRoute>
                <Peneira />
              </ProtectedRoute>
            }
          />

          <Route
            path="/peneiras/:id"
            element={
              <ProtectedRoute>
                <PeneiraDetalhes />
              </ProtectedRoute>
            }
          />

          <Route
            path="/peneiras/:id/editor-times"
            element={
              <ProtectedRoute>
                <EditorTimes />
              </ProtectedRoute>
            }
          />

          <Route
            path="/players"
            element={
              <ProtectedRoute>
                <Players />
              </ProtectedRoute>
            }
          />

          <Route
            path="/jogadores/:id"
            element={
              <ProtectedRoute>
                <PlayerInfo />
              </ProtectedRoute>
            }
          />

          <Route
            path="/jogadores/:id/edit"
            element={
              <ProtectedRoute>
                <PlayerEdit />
              </ProtectedRoute>
            }
          />

          {/* Redirecionamentos */}
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </div>
    </Router>
  );
};

export default App;
