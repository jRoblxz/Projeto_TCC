import React, { useEffect, useRef, useState } from "react";
import {
  MapContainer,
  TileLayer,
  CircleMarker,
  Popup,
  useMap,
} from "react-leaflet";
import "leaflet/dist/leaflet.css";
import Layout from "@/components/layouts/Layout";
import { api } from "@/config/api";
import { Map as MapIcon, Loader2, Minimize, Maximize } from "lucide-react";

interface LocationData {
  cidade: string;
  latitude: number;
  longitude: number;
  total: number;
}

// componente para o mapa não bugar ao entrar em Fullscreen
const MapSizeInvalidator = ({ isFullscreen }: { isFullscreen: boolean }) => {
  const map = useMap();
  useEffect(() => {
    setTimeout(() => map.invalidateSize(), 200);
  }, [isFullscreen, map]);
  return null;
};

const UserMap: React.FC = () => {
  const [locations, setLocations] = useState<LocationData[]>([]);
  const [loading, setLoading] = useState(true);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const mapContainerWrapperRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const loadMapData = async () => {
      try {
        const response = await api.get("/users/map-stats");
        setLocations(response.data);
      } catch (error) {
        console.error("Erro ao carregar mapa", error);
      } finally {
        setLoading(false);
      }
    };
    loadMapData();
  }, []);

  // NOVO: Ouvinte de evento para quando o usuário aperta "ESC" para sair do Fullscreen
  useEffect(() => {
    const onFullscreenChange = () => {
      setIsFullscreen(!!document.fullscreenElement);
    };

    document.addEventListener("fullscreenchange", onFullscreenChange);
    return () => {
      document.removeEventListener("fullscreenchange", onFullscreenChange);
    };
  }, []);

  // Função para alternar o ecrã inteiro nativamente
  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      mapContainerWrapperRef.current?.requestFullscreen().catch((err) => {
        console.log(`Erro ao tentar fullscreen: ${err.message}`);
      });
    } else {
      document.exitFullscreen();
    }
  };

  return (
    <Layout>
      <div className="max-w-[1200px] mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <div className="flex justify-between items-center bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
          <div>
            <h1 className="text-2xl font-bold text-brand-primary dark:text-white flex items-center gap-2">
              <MapIcon className="text-brand-darkred" /> Mapa de Talentos
            </h1>
            <p className="text-sm text-gray-500 mt-1">
              Distribuição geográfica dos jogadores inscritos.
            </p>
          </div>
        </div>

        <div className="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden h-[600px] relative z-0">
          {loading && (
            <div className="absolute inset-0 bg-white/50 backdrop-blur-sm z-[1000] flex items-center justify-center">
              <Loader2 className="animate-spin text-brand-primary" size={40} />
            </div>
          )}
          <div
            ref={mapContainerWrapperRef}
            className={`relative w-full overflow-hidden rounded-lg border dark:border-gray-700 ${isFullscreen ? "h-screen bg-black" : "h-[590px]"}`}
          >
            {/* CORREÇÃO AQUI: z-[1000] para o botão ficar em cima do mapa */}
            <button
              onClick={toggleFullscreen}
              className="absolute top-[10px] right-[10px] z-[1000] bg-white dark:bg-gray-800 p-2 rounded shadow-md border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition"
              title={isFullscreen ? "Sair da tela cheia" : "Tela cheia"}
            >
              {isFullscreen ? <Minimize size={20} /> : <Maximize size={20} />}
            </button>

            <MapContainer
              center={[-22.1225, -51.3852]}
              zoom={12}
              style={{ height: "100%", width: "100%" }}
            >
              <TileLayer
                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
              />

              <MapSizeInvalidator isFullscreen={isFullscreen} />

              {locations.map((loc, idx) => (
                <CircleMarker
                  key={idx}
                  center={[loc.latitude, loc.longitude]}
                  radius={10 + loc.total * 2}
                  pathOptions={{
                    fillColor: "#8B0000",
                    color: "#14244D",
                    weight: 2,
                    fillOpacity: 0.6,
                  }}
                >
                  <Popup>
                    <div className="p-2">
                      <h4 className="font-bold text-brand-primary">
                        {loc.cidade}
                      </h4>
                      <p className="text-sm font-medium">
                        {loc.total} Jogadores Inscritos
                      </p>
                    </div>
                  </Popup>
                </CircleMarker>
              ))}
            </MapContainer>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default UserMap;
