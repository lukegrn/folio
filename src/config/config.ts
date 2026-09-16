import { LogLevel } from "tslog";

interface DatabaseConfig {
  host: string;
  port: number;
  database: string;
  username: string;
  password: string;
}

interface LogConfig {
  minLevel: LogLevel;
}

interface ApplicationConfig {
  port: number;
}

interface Config {
  database: DatabaseConfig;
  log: LogConfig;
  application: ApplicationConfig;
}

let config: Config | undefined;

// Only used for testing
export const resetConfig = () => {
  config = undefined;
};

const req = (envVal: string) => {
  const v = process.env[envVal];
  if (v) {
    return v;
  }

  throw new Error(`Required value ${envVal} not found`);
};

const num = (v: string) => {
  const i = parseInt(v);
  if (isNaN(i)) {
    throw new Error(`Unable to parse string ${v} as int`);
  }

  return i;
};

const enumOf = <T extends Record<string, string | number>>(v: string, e: T) => {
  if (!(v in e)) {
    throw new Error(`String value ${v} is not a member of enum`);
  }

  return e[v as keyof typeof e];
};

export const registerConfig = () => {
  config = {
    database: {
      host: req("DB_HOST"),
      port: num(req("DB_PORT")),
      database: req("DB_DATABASE"),
      username: req("DB_USER"),
      password: req("DB_PASS"),
    },
    log: {
      minLevel: enumOf(req("LOG_MIN_LEVEL"), LogLevel),
    },
    application: {
      port: num(req("APPLICATION_PORT")),
    },
  };
};

export const getConfig = () => {
  if (config === undefined) {
    registerConfig();
  }

  return config as Config;
};
