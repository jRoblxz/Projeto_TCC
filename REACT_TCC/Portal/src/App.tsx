import React, { FC } from "react";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";
import { Toaster } from "react-hot-toast";
import ProtectedRoute from "@/components/ProtectedRoute";
import "@/App.css";

// ---------- Páginas ----------//
import Login from "@/pages/Login";
import Dashboard from "@/pages/Dashboard";
import InscricaoForm from "@/pages/Inscricoes/InscricaoForm";
import Confirmacao from "@/pages/Inscricoes/Confirmacao";
import Instrucao from "@/pages/Inscricoes/Instrucao";

//Players
import Players from "@/pages/Jogadores/Players";
import PlayerInfo from "@/pages/Jogadores/PlayerInfo";
import PlayerEdit from "@/pages/Jogadores/PlayerEdit";

//Peneiras
import Peneira from "@/pages/Peneiras/Peneiras";
import PeneiraDetalhes from "@/pages/Peneiras/PeneiraDetalhes";
import EditorTimes from "@/pages/Peneiras/EditorTimes";


//ANalises
import TrackingPartida from "@/pages/Analises/TrackingPartida";
import AnaliseDados from "@/pages/Analises/AnaliseDados";
import AtletasDestaque from "@/pages/Analises/AtletasDestaques";

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
