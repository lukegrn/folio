import { test, mock, beforeEach, expect } from "bun:test";
import { init, send } from "../email";
import { getConfig } from "../../config/config";
import type { Response } from "express";

const mockVerify = mock();
const mockSendMail = mock();
const mockLogInfoForRequest = mock();
const mockLogErrorForRequest = mock();

const transport = {
  verify: mockVerify,
  sendMail: mockSendMail,
};

const mockCreateTransport = mock(() => transport);

mock.module("../../logger", () => {
  return {
    logInfoForRequest: mockLogInfoForRequest,
    logErrorForRequest: mockLogErrorForRequest,
  };
});

beforeEach(() => {
  mock.module("nodemailer", () => {
    return {
      default: {
        createTransport: mockCreateTransport,
        verify: mockVerify,
      },
    };
  });

  mockVerify.mockClear();
  mockCreateTransport.mockClear();
  mockSendMail.mockClear();
  mockLogInfoForRequest.mockClear();
  mockLogErrorForRequest.mockClear();
});

test("initializes with config value", async () => {
  await init();

  const conf = getConfig();

  expect(mockCreateTransport).toHaveBeenCalledTimes(1);
  expect(mockCreateTransport).toHaveBeenCalledWith({
    host: conf.email.host,
    port: conf.email.port,
    secure: false,
    auth: {
      user: conf.email.user,
      pass: conf.email.pass,
    },
  });
});

test("verifies the transport", async () => {
  await init();

  expect(mockVerify).toHaveBeenCalledTimes(1);
});

test("sends a message and reports success", async () => {
  await init();

  const payload = {
    from: "from@test.com",
    to: "to@test.com",
    subject: "test subject",
    message: "test message",
  };

  const success = await send({ ...payload, res: {} as Response });

  expect(mockSendMail).toHaveBeenCalledTimes(1);
  expect(mockSendMail).toHaveBeenCalledWith({
    from: payload.from,
    to: payload.to,
    subject: payload.subject,
    text: payload.message,
    html: `<p>${payload.message}</p>`,
  });

  expect(mockLogInfoForRequest).toHaveBeenCalledTimes(1);

  expect(success).toBeTrue();
});

test("on error reports error", async () => {
  mock.module("nodemailer", () => {
    return {
      default: {
        createTransport: () => ({
          verify: mockVerify,
          sendMail: mock(() => {
            throw new Error("test error");
          }),
        }),
        verify: mockVerify,
      },
    };
  });

  await init();

  const payload = {
    from: "from@test.com",
    to: "to@test.com",
    subject: "test subject",
    message: "test message",
  };

  const success = await send({ ...payload, res: {} as Response });

  expect(mockLogErrorForRequest).toHaveBeenCalledTimes(1);

  expect(success).toBeFalse();
});
