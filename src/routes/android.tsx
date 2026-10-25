// Download público do APK Android (distribuição direta, fora da Play Store).
import { Hono } from 'hono';
import { AppAndroidPage } from '../pages/AppAndroid';
import type { Vars } from '../lib/session';
import type { AppBindings } from '../lib/bindings';

const android = new Hono<{ Bindings: AppBindings; Variables: Vars }>();

android.get('/app', async (c) => {
  const user = c.get('user');
  const alertas = user
    ? await c.env.DB.prepare('SELECT COUNT(*) AS n FROM alerts WHERE user_id = ? AND lido = 0')
        .bind(user.id)
        .first<{ n: number }>()
    : null;

  return c.html(<AppAndroidPage user={user} alertas={alertas?.n ?? 0} />);
});

export default android;
