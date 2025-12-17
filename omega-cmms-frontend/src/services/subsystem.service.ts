import { apiClient } from '@/lib/api/client';
import { ApiResponse, Subsystem } from '@/types';

export class SubsystemService {
  // Entry 7 Fix: Subsystem Management
  async getSubsystems(params?: {
    page?: number;
    per_page?: number;
    building_id?: number;
    type?: string;
    facility_id?: number;
    search?: string;
  }): Promise<ApiResponse<Subsystem[]>> {
    const response = await apiClient.get<ApiResponse<Subsystem[]>>('/subsystems', { params });
    return response.data;
  }

  async getSubsystem(id: number): Promise<ApiResponse<Subsystem>> {
    const response = await apiClient.get<ApiResponse<Subsystem>>(`/subsystems/${id}`);
    return response.data;
  }

  async createSubsystem(data: Partial<Subsystem>): Promise<ApiResponse<Subsystem>> {
    const response = await apiClient.post<ApiResponse<Subsystem>>('/subsystems', data);
    return response.data;
  }

  async updateSubsystem(id: number, data: Partial<Subsystem>): Promise<ApiResponse<Subsystem>> {
    const response = await apiClient.put<ApiResponse<Subsystem>>(`/subsystems/${id}`, data);
    return response.data;
  }

  async deleteSubsystem(id: number): Promise<ApiResponse<void>> {
    const response = await apiClient.delete<ApiResponse<void>>(`/subsystems/${id}`);
    return response.data;
  }
}

export const subsystemService = new SubsystemService();