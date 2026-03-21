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
import InscricaoForm from "./pages/InscricaoForm";
import Confirmacao from "./pages/Confirmacao";
import Instrucao from "./pages/Instrucao";
import TrackingPartida from "./pages/TrackingPartida";
import AnaliseDados from "./pages/AnaliseDados";
import AtletasDestaque from "./pages/AtletasDestaques";

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
            path="/instrucoes"
            element={
              <ProtectedRoute>
                <Instrucao />
              </ProtectedRoute>
            }
          />
          <Route
            path="/inscricao"
            element={
              <ProtectedRoute>
                <InscricaoForm />
              </ProtectedRoute>
            }
          />
          <Route
            path="/confirmacao"
            element={
              <ProtectedRoute>
                <Confirmacao />
              </ProtectedRoute>
            }
          />

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

          <Route
            path="/tracking"
            element={
              <ProtectedRoute>
                <TrackingPartida />
              </ProtectedRoute>
            }
          />

          <Route
            path="/analise"
            element={
              <ProtectedRoute>
                <AnaliseDados />
              </ProtectedRoute>
            }
          />

          <Route
            path="/destaques"
            element={
              <ProtectedRoute>
                <AtletasDestaque />
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
