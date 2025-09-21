import { Component } from '@angular/core';

import { CommonModule } from '@angular/common'; // Módulo para o ngIf
import { FormsModule } from '@angular/forms'; // Módulo para o ngModel
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})

export class LoginComponent {
  credentials = { email: '', password: '' };
  loginError = false;

  constructor(private apiService: ApiService) { }

  onLogin(): void {
    this.apiService.login(this.credentials).subscribe(
      (response) => {
        console.log('Login bem-sucedido!', response);
      },
      (error) => {
        console.error('Erro no login!', error);
        this.loginError = true;
      }
    );
  }
}
