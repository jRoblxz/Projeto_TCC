// hooks/useVideoJob.ts
// Hook React para polling do status do job

import { useEffect, useState, useCallback } from 'react'

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
    if (!jobId) return
    try {
      const res = await fetch(`/api/video-jobs/${jobId}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept': 'application/json',
        },
      })
      if (!res.ok) throw new Error('Erro ao buscar status')
      const data: VideoJob = await res.json()
      setJob(data)
      return data.status
    } catch (err) {
      setError('Erro ao verificar status do job')
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
