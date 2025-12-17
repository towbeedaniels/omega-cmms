// app/page.tsx
import Dashboard from '@/components/dashboard/Dashboard'

export default function Home() {
  return (
    <main className="min-h-screen bg-gray-50">
      <Dashboard />
      {/* Or your main landing page component */}
    </main>
  )
}