<div class="modal-overlay" id="authModal">
  <div class="modal">
    <button class="modal-close" id="modalClose">✕</button>
    <div class="modal-tabs">
      <button class="modal-tab active" data-tab="login">Se connecter</button>
      <button class="modal-tab" data-tab="register">S'inscrire</button>
    </div>

    <!-- CONNEXION -->
    <div class="modal-panel active" id="panel-login">
      <p class="modal-title">Bon retour 👋</p>
      <p class="modal-subtitle">Connectez-vous à votre espace</p>
      <div class="form-alert error"   id="loginError"></div>
      <div class="form-alert success" id="loginSuccess"></div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" id="loginEmail" placeholder="exemple@email.com" autocomplete="email">
      </div>
      <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" id="loginPassword" placeholder="••••••••" autocomplete="current-password">
      </div>
      <button class="btn btn-primary btn-full" id="loginBtn">Se connecter →</button>
      <p class="form-switch">Pas encore de compte ? <a href="#" id="switchToRegister">S'inscrire</a></p>
      <p class="form-switch" style="margin-top:.4rem"><a href="#" id="forgotPasswordLink" style="color:var(--muted);font-size:.75rem">Mot de passe oublié ?</a></p>
    </div>

    <!-- INSCRIPTION -->
    <div class="modal-panel" id="panel-register">
      <p class="modal-title">Créer un compte</p>
      <p class="modal-subtitle">Rejoignez Web4All gratuitement</p>
      <div class="form-alert error"   id="registerError"></div>
      <div class="form-alert success" id="registerSuccess"></div>

      <!-- Sélecteur de rôle -->
      <div class="role-selector">
        <button type="button" class="role-choice active" data-role="etudiant">
          <span class="role-choice-icon">🎓</span>
          <span class="role-choice-label">Étudiant</span>
          <span class="role-choice-desc">Je cherche un stage ou une alternance</span>
        </button>
        <button type="button" class="role-choice" data-role="pilote">
          <span class="role-choice-icon">🏢</span>
          <span class="role-choice-label">Pilote</span>
          <span class="role-choice-desc">Je propose des offres pour mon entreprise</span>
        </button>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Prénom</label>
          <input type="text" id="registerPrenom" placeholder="Jean" autocomplete="given-name">
        </div>
        <div class="form-group">
          <label>Nom</label>
          <input type="text" id="registerNom" placeholder="Dupont" autocomplete="family-name">
        </div>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" id="registerEmail" placeholder="exemple@email.com" autocomplete="email">
      </div>

      <!-- Champ étudiant : promotion -->
      <div class="form-group" id="fieldPromo">
        <label>Promotion</label>
        <select id="registerPromo"><option value="">Chargement…</option></select>
      </div>

      <!-- Champ pilote : sélection entreprise existante ou nouvelle -->
      <div class="form-group" id="fieldEntreprise" style="display:none">
        <label>Entreprise *</label>
        <select id="registerEntrepriseSelect" onchange="onEntrepriseSelectChange()">
          <option value="">— Choisir une entreprise —</option>
          <!-- "Autre" sera injecté en premier par le JS -->
        </select>
        <small style="color:var(--muted);font-size:.72rem;margin-top:.3rem;display:block;line-height:1.4">
          ⚠️ Votre compte sera activé après validation par un administrateur.
        </small>
      </div>

      <!-- Champ nom libre (si "Autre" sélectionné) -->
      <div class="form-group" id="fieldNomEntrepriseLibre" style="display:none">
        <label>Nom de votre entreprise *</label>
        <input type="text" id="registerNomEntreprise"
               placeholder="ex : TechNormandie"
               autocomplete="organization">
        <small style="color:var(--muted);font-size:.72rem;margin-top:.2rem;display:block">
          L'entreprise sera créée lors de la validation de votre compte.
        </small>
      </div>

      <div class="form-group">
        <label>Mot de passe <small style="color:var(--muted);font-weight:400">(min. 6 car.)</small></label>
        <input type="password" id="registerPassword" placeholder="••••••••" autocomplete="new-password">
      </div>
      <button class="btn btn-primary btn-full" id="registerBtn">Créer mon compte →</button>
      <p class="form-switch">Déjà un compte ? <a href="#" id="switchToLogin">Se connecter</a></p>
    </div>

    <!-- ── PANEL MOT DE PASSE OUBLIÉ ── -->
    <div class="modal-panel" id="panel-forgot">
      <p class="modal-title">Mot de passe oublié</p>
      <p class="modal-subtitle">Entrez votre email pour recevoir un lien de réinitialisation.</p>
      <div class="form-alert" id="forgotAlert"></div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" id="forgotEmail" placeholder="votre@email.fr" autocomplete="email">
      </div>
      <button class="btn btn-primary btn-full" id="forgotBtn">Envoyer le lien →</button>
      <p class="form-switch" style="margin-top:.75rem"><a href="#" id="backToLogin">← Retour à la connexion</a></p>
    </div>
  </div>
</div>
