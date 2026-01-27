import React, { useState, useEffect, FormEvent } from "react";
import Layout from "@/components/layouts/Layout";
import Card from "@/components/ui/Card";
import { api } from "../config/api"; 
import {
  Plus,
  Edit,
  Trash,
  Search,
  Loader2,
  Calendar,
  MapPin,
  Info,
  X,
  Eye
} from "lucide-react";
import toast from "react-hot-toast";

interface PeneiraData {
  id: number;
  nome_evento: string;
  data_evento: string;
  local: string;
  status: string;
  sub_divisao: string;
  descricao: string;
}

const Peneiras: React.FC = () => {
  // ================= ESTADOS =================
  const [data, setData] = useState<PeneiraData[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [showForm, setShowForm] = useState<boolean>(false);
  const [editing, setEditing] = useState<boolean>(false); 
  const [isDeleting, setIsDeleting] = useState<boolean>(false); 
  const [itemToDelete, setItemToDelete] = useState<PeneiraData | null>(null);
  const [searchTerm, setSearchTerm] = useState("");

  // Estado do Formulário
  const initialFormData = {
    id: 0,
    nome_evento: "",
    data_evento: "",
    local: "",
    status: "AGENDADA",
    sub_divisao: "",
    descricao: ""
  };
  const [formData, setFormData] = useState<PeneiraData>(initialFormData);

  // ================= LOAD =================
  const loadData = async () => {
    setLoading(true);
    try {
      const response = await api.get('/peneiras');
      const lista = response.data.data || response.data;
      setData(lista);
    } catch (error) {
      console.error("Erro ao carregar:", error);
      toast.error("Erro ao carregar lista de peneiras.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadData();
  }, []);

  // ================= HANDLES =================

  const handleNew = () => {
    setFormData(initialFormData);
    setEditing(false);
    setIsDeleting(false);
    setShowForm(true);
  };

  const handleEdit = (item: PeneiraData) => {
    // Formata data para o input datetime-local
    let dataFormatada = "";
    if (item.data_evento) {
        const d = new Date(item.data_evento);
        if (!isNaN(d.getTime())) {
             dataFormatada = d.toISOString().slice(0, 16); 
        } else {
             dataFormatada = String(item.data_evento).replace(" ", "T").slice(0, 16);
        }
    }

    setFormData({ 
        ...item, 
        data_evento: dataFormatada,
        sub_divisao: item.sub_divisao || "" // Garante que não seja null
    });
    setEditing(true);
    setShowForm(true);
  };

  const handleDelete = (item: PeneiraData) => {
    setItemToDelete(item);
    setIsDeleting(true);
  };

  const handleCancel = () => {
    setShowForm(false);
    setIsDeleting(false);
    setItemToDelete(null);
    setEditing(false);
  };

  const confirmDelete = async () => {
    if (!itemToDelete) return;
    const toastId = toast.loading("Excluindo...");
    try {
      await api.delete(`/peneiras/${itemToDelete.id}`);
      toast.success("Excluído com sucesso!");
      setIsDeleting(false);
      setItemToDelete(null);
      loadData();
    } catch (error) {
      console.error(error);
      toast.error("Erro ao excluir.");
    } finally {
      toast.dismiss(toastId);
    }
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    // [CORREÇÃO] Validação Obrigatória para sub_divisao
    if (!formData.nome_evento || !formData.data_evento || !formData.local || !formData.sub_divisao) {
      toast.error("Preencha todos os campos obrigatórios (*)");
      return;
    }

    setLoading(true);
    const toastId = toast.loading("Salvando...");

    try {
      // Monta o objeto garantindo que sub_divisao seja string
      const payload = {
          ...formData,
          sub_divisao: formData.sub_divisao || "Geral" // Fallback de segurança
      };

      if (editing) {
        await api.put(`/peneiras/${formData.id}`, payload);
        toast.success("Peneira atualizada!");
      } else {
        const { id, ...dadosParaSalvar } = payload; 
        await api.post('/peneiras', dadosParaSalvar);
        toast.success("Peneira cadastrada!");
      }

      setShowForm(false);
      loadData();
    } catch (error: any) {
      console.error(error);
      const msg = error.response?.data?.message || "Erro ao salvar.";
      toast.error(msg);
    } finally {
      setLoading(false);
      toast.dismiss(toastId);
    }
  };

  const filteredData = data.filter(item => 
    String(item.nome_evento || "").toLowerCase().includes(searchTerm.toLowerCase()) ||
    String(item.local || "").toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <Layout>
      <div className="space-y-6 h-fit relative p-6">
        
        {loading && (
          <div className="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-50 rounded-lg">
            <Loader2 className="h-10 w-10 text-blue-600 animate-spin" />
          </div>
        )}

        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
          Gerenciamento de Peneiras
        </h1>

        <Card className="p-6">
          
          {!showForm ? (
            <div className="space-y-6">
              <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                <div className="relative w-full md:w-72">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                  <input 
                    type="text" 
                    placeholder="Buscar peneira..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                  />
                </div>
                <button
                  onClick={handleNew}
                  className="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition w-full md:w-auto"
                >
                  <Plus className="h-4 w-4" /> Cadastrar
                </button>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {filteredData.map((item) => (
                  <div key={item.id} className="border rounded-xl p-4 shadow-sm hover:shadow-md transition bg-white relative group">
                    <div className="flex justify-between items-start mb-2">
                       <h3 className="font-bold text-lg text-[#14244D]">{item.nome_evento}</h3>
                       <span className="text-xs font-bold px-2 py-1 bg-gray-100 rounded uppercase">{item.status}</span>
                    </div>
                    <div className="text-sm text-gray-600 space-y-1 mb-4">
                       <p className="flex items-center gap-2"><Calendar className="h-3 w-3"/> {new Date(item.data_evento).toLocaleDateString()}</p>
                       <p className="flex items-center gap-2"><MapPin className="h-3 w-3"/> {item.local}</p>
                       <p className="flex items-center gap-2"><Info className="h-3 w-3"/> {item.sub_divisao}</p>
                    </div>
                    
                    <div className="flex gap-2 border-t pt-3">
                      <button 
                        onClick={() => handleEdit(item)}
                        className="flex-1 flex items-center justify-center gap-1 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 py-1.5 rounded text-sm font-medium transition"
                      >
                        <Edit className="h-3 w-3" /> Editar
                      </button>
                      <button 
                        onClick={() => handleDelete(item)}
                        className="flex-1 flex items-center justify-center gap-1 bg-red-100 text-red-700 hover:bg-red-200 py-1.5 rounded text-sm font-medium transition"
                      >
                        <Trash className="h-3 w-3" /> Excluir
                      </button>
                    </div>
                  </div>
                ))}
                {filteredData.length === 0 && (
                  <p className="text-gray-500 col-span-full text-center py-10">Nenhuma peneira encontrada.</p>
                )}
              </div>
            </div>
          ) : (
            <div className="max-w-2xl mx-auto">
              <div className="flex justify-between items-center mb-6 border-b pb-4">
                <h2 className="text-xl font-semibold">
                  {editing ? "Editar Peneira" : "Cadastrar Peneira"}
                </h2>
                <button onClick={handleCancel} className="p-1 hover:bg-gray-100 rounded-full">
                    <X className="h-5 w-5 text-gray-500" />
                </button>
              </div>

              <form onSubmit={handleSubmit} className="space-y-4">
                
                {editing && (
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <input type="text" disabled value={formData.id} className="w-full border rounded p-2 bg-gray-100" />
                  </div>
                )}

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Título do Evento *</label>
                    <input 
                      type="text" 
                      required
                      value={formData.nome_evento}
                      onChange={(e) => setFormData({...formData, nome_evento: e.target.value})}
                      className="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                    />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Data e Hora *</label>
                        <input 
                          type="datetime-local" 
                          required
                          value={formData.data_evento}
                          onChange={(e) => setFormData({...formData, data_evento: e.target.value})}
                          className="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Local *</label>
                        <input 
                          type="text" 
                          required
                          value={formData.local}
                          onChange={(e) => setFormData({...formData, local: e.target.value})}
                          className="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select 
                          value={formData.status}
                          onChange={(e) => setFormData({...formData, status: e.target.value})}
                          className="w-full border rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 outline-none"
                        >
                          <option value="AGENDADA">Agendada</option>
                          <option value="EM_ANDAMENTO">Em Andamento</option>
                          <option value="FINALIZADA">Finalizada</option>
                          <option value="CANCELADA">Cancelada</option>
                        </select>
                    </div>
                    <div>
                        {/* [CORREÇÃO] Campo agora é OBRIGATÓRIO */}
                        <label className="block text-sm font-medium text-gray-700 mb-1">Subdivisão / Info *</label>
                        <input 
                          type="text" 
                          required
                          placeholder="Ex: Sub-15"
                          value={formData.sub_divisao || ""}
                          onChange={(e) => setFormData({...formData, sub_divisao: e.target.value})}
                          className="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                    </div>
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea 
                      rows={3}
                      value={formData.descricao || ""}
                      onChange={(e) => setFormData({...formData, descricao: e.target.value})}
                      className="w-full border rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                    />
                </div>

                <div className="flex justify-end gap-3 pt-4">
                  <button
                    type="button"
                    onClick={handleCancel}
                    className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                  >
                    {editing ? "Salvar Alterações" : "Cadastrar"}
                  </button>
                </div>
              </form>
            </div>
          )}
        </Card>

        {isDeleting && itemToDelete && (
            <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div className="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm text-center">
                    <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4">
                        <Trash className="h-6 w-6 text-red-600" />
                    </div>
                    <h3 className="text-lg font-bold text-gray-900 mb-2">Confirmar Exclusão</h3>
                    <p className="text-sm text-gray-500 mb-6">
                        Deseja realmente excluir <strong>{itemToDelete.nome_evento}</strong>?
                    </p>
                    <div className="flex gap-3 justify-center">
                        <button 
                            onClick={handleCancel}
                            className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition"
                        >
                            Cancelar
                        </button>
                        <button 
                            onClick={confirmDelete}
                            className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition"
                        >
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        )}
      </div>
    </Layout>
  );
};

export default Peneiras;