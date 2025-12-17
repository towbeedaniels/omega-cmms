import { apiClient } from '@/lib/api/client';
import { ApiResponse, Facility } from '@/types';

export class FacilityService {
  async getFacilities(params?: any): Promise<ApiResponse<Facility[]>> {
    const response = await apiClient.get<ApiResponse<Facility[]>>('/facilities', { params });
    return response.data;
  }
}

export const facilityService = new FacilityService();