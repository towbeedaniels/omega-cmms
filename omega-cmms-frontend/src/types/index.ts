// Basic types - we'll expand these later
export interface User {
  id: number;
  name: string;
  email: string;
  role_id: number;
  facility_scope: number[];
  access_level: 'view' | 'edit' | 'admin';
  is_active: boolean;
}

export interface AuthResponse {
  success: boolean;
  user: User;
  token: string;
  abilities: string[];
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
  meta?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

// Add other types as needed
export interface Facility {
  id: number;
  name: string;
  code: string;
  type: string;
  color_code: string;
  is_active: boolean;
}

export interface WorkOrder {
  id: number;
  work_order_number: string;
  title: string;
  priority: string;
  status: string;
  asset?: {
    name: string;
  };
  facility?: {
    name: string;
  };
  completed_at?: string;
  due_date?: string;
}