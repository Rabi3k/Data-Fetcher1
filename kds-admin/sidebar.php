          <div class="sidebar-sticky">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link" href="/admin">
                  <span data-feather="home"></span>
                  <?php _e("dashboard", "Dashboard") ?> <span class="sr-only">(<?php _e("current", "current") ?>)</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/users">
                  <span data-feather="users"></span>
                  <?php _e("users", "Users") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/user-profiles">
                  <span data-feather="user-x"></span>
                  <?php _e("user_profiles", "User Profiles") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/companies">
                  <span data-feather="codesandbox"></span>
                  <?php _e("companies", "Companies") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/restaurants">
                  <span data-feather="coffee"></span>
                  <?php _e("restaurants", "Restaurants") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="bar-chart-2"></span>
                  <?php _e("reports", "Reports") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/integrations">
                  <span data-feather="layers"></span>
                  <?php _e("integrations", "Integrations") ?>
                </a>
              </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span><?php _e("saved_reports", "Saved reports") ?></span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  <?php _e("current_month", "Current month") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  <?php _e("last_quarter", "Last quarter") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  <?php _e("social_engagement", "Social engagement") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  <?php _e("year_end_sale", "Year-end sale") ?>
                </a>
              </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span><?php _e("settings", "Settings") ?></span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="settings"></span>
              </a>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <a class="nav-link" href="/admin/database-setup">
                  <span data-feather="database"></span>
                  <?php _e("database_connections", "Database Connections") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/email-setup">
                  <span data-feather="send"></span>
                  <?php _e("smtp_settings", "Smtp Settings") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/update">
                  <span data-feather="download-cloud"></span>
                  <?php _e("check_updates", "Check Updates") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/translations">
                  <i class="bi bi-translate"></i>
                  <?php _e("admin_translations", "Translations") ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/admin/loggy">
                  <span data-feather="cpu"></span>
                  <?php _e("loggy", "Loggy") ?>
                </a>
              </li>
            </ul>

          </div>