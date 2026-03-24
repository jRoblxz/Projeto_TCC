// hooks/useVideoJob.ts
import { useEffect, useState, useCallback } from 'react'
import { api } from '@/config/api' // <-- Importe a sua instância do axios aqui!

export type JobStatus = 'pending' | 'processing' | 'done' | 'failed'

export interface VideoJob {
  id: number
  status: JobStatus
  video_url: string | null
  csv_url: string | null
  total_frames: number | null
  error: string | null
  created_at: string
  finished_at: string | null
}

const POLL_INTERVAL = 5000 // 5 segundos

export function useVideoJob(jobId: number | null) {
  const [job, setJob] = useState<VideoJob | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const fetchJob = useCallback(async () => {
    if (!jobId) return;
    
    try {
      // O Axios já coloca o "http://.../api/v1" e o token de autenticação automaticamente!
      const response = await api.get(`/video-jobs/${jobId}`);
      
      // No axios, os dados ficam dentro da propriedade .data
      const data: VideoJob = response.data; 
      
      setJob(data);
      return data.status;
    } catch (err) {
      console.error("Erro ao buscar status do job:", err);
      setError('Erro ao verificar status do job');
    }
  }, [jobId])

  useEffect(() => {
    if (!jobId) return
    setLoading(true)
    fetchJob().then(() => setLoading(false))

    // Polling enquanto job estiver em processamento
    const interval = setInterval(async () => {
      const status = await fetchJob()
      if (status === 'done' || status === 'failed') {
        clearInterval(interval)
      }
    }, POLL_INTERVAL)

    return () => clearInterval(interval)
  }, [jobId, fetchJob])

  return { job, loading, error }
}