import { apiClient } from '@/lib/api/client';
import { ApiResponse, WorkOrder } from '@/types';

export class WorkOrderService {
  async getWorkOrders(params?: any): Promise<ApiResponse<WorkOrder[]>> {
    const response = await apiClient.get<ApiResponse<WorkOrder[]>>('/work-orders', { params });
    return response.data;
  }
}

export const workOrderService = new WorkOrderService();